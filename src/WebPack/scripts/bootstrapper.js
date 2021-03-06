"use strict";

// standard libraries
const fs = require('fs');
const execSync = require('child_process').execSync;

// packages
const dotenv = require('dotenv');
const Watchpack = require("watchpack");

class Bootstrapper {
    constructor(config, paths) {
        this.config = config;
        this.paths = paths;
    }

    run() {
        this.setEnvironment();
        this.keepFresh();
        this.prepare();
        this.load();
    }

    setEnvironment() {
        let env = dotenv.parse(fs.readFileSync('.env'));

        /**
         * You can override default environment by using command line:
         *
         *      npm run webpack -- --env.APP_ENV=testing
         */
        Object.assign(process.env,
            {
                NO_WEBPACK_PREPARE: '0',
                NO_WEBPACK_SOURCE_MAPS: '0',
                APP_ENV: 'production'
            },
            {
                NO_WEBPACK_SOURCE_MAPS: env.NO_WEBPACK_SOURCE_MAPS,
                APP_ENV: env.APP_ENV
            });

        process.argv.slice(2).forEach(arg => {
            if (!arg.startsWith('--env.APP_ENV=')) {
                return;
            }

            process.env.APP_ENV = arg.substr('--env.APP_ENV='.length);
        });
    }

    keepFresh() {
        if (process.argv.indexOf('--watch') != -1) {
            let wp = new Watchpack({
                // options:
                aggregateTimeout: 300,
                // fire "aggregated" event when after a change for 1000ms no additional change occurred
                // aggregated defaults to undefined, which doesn't fire an "aggregated" event

                //poll: true,
                // poll: true - use polling with the default interval
                // poll: 10000 - use polling with an interval of 10s
                // poll defaults to undefined, which prefer native watching methods
                // Note: enable polling when watching on a network path

                //ignored: /node_modules/
                // anymatch-compatible definition of files/paths to be ignored
                // see https://github.com/paulmillr/chokidar#path-filtering
            });

            wp.watch(['.env', '.env.testing', 'bootstrap.php', 'fresh', 'run', 'public/index.php'],
                ['app', 'config', 'vendor']);
            wp.on("aggregated", () => {
                execSync('php fresh', {stdio: 'inherit'});
            });
        }
        else {
            execSync('php fresh', {stdio: 'inherit'});
        }
    }

    prepare() {
        if (!parseInt(process.env.NO_WEBPACK_PREPARE)) {
            execSync('php run config:webpack', {stdio: 'inherit'});
        }
    }

    load() {
        const settings = {
            /**
             * List of all modules sorted by dependency. Index in this list is internal module name.
             * Structure of single module:
             *
             *      name - internal module name
             *      path - location of module directory, relative to root directory
             */
            modules: [],

            /**
             * List of all themes. Index in this list is internal theme name. Structure of individual theme:
             *
             *      name - internal theme name
             *      parent_theme - name of parent theme or empty if theme has no parent theme
             *      area - "frontend" or "adminhtml"
             *      path - location of theme directory, relative to root directory
             */
            themes: [],

            /**
             * List of all areas. Index in this list is internal area name. Structure of individual area:
             *
             *      name - internal theme name
             *      parent_area - name of parent area or empty if area has no parent area
             *      resource_path
             */
            areas: [],

            /**
             * List of all targets in `pub` directory/ structure of individual target:
             *
             *      area - "frontend" or "adminhtml"
             *      theme - internal theme name
             */
            targets: []
        };
        const configFilePath = this.paths.getTempPath('webpack.json');
        Object.assign(settings, JSON.parse(fs.readFileSync(configFilePath, 'utf8')));

        this.config.modules = this.loadMap(settings.modules);
        this.config.themes = this.loadMap(settings.themes);
        settings.themes.forEach(theme => {
            if (theme.definitions) {
                theme.definitions = this.loadMap(theme.definitions);
            }
        });
        this.config.areas = this.loadMap(settings.areas);
        this.config.targets = settings.targets;
        this.config.data = settings.data;
    }

    loadMap(namedArray) {
        const map = new Map();

        namedArray.forEach(item => {
            map.set(item.name, item);
        });

        return map;
    }
}

module.exports = new Bootstrapper(require('./config'), require('./paths'));
