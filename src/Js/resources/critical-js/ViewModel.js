import find from "./find";
import view_models from "./vars/view_models";

export default class ViewModel {
    constructor(selector, model) {
        console.warn(`OBSOLETE: ${this.constructor.name} view model`);

        this.model = model || {};
        this.element = find(selector);

        if (view_models.has(this.element)) {
            let viewModels = view_models.get(this.element);
            viewModels.push(this);
            view_models.set(this.element, viewModels);
        }
        else {
            view_models.set(this.element, [this]);
        }

        this.onAttach();
    }

    onAttach() {
        if (this.onResize) {
            this.onResize();
            window.addEventListener('resize', this._onResize = this.onResize.bind(this));
        }
    }

    onDetach() {
        if (this.onResize) {
            window.removeEventListener('resize', this._onResize);
        }
    }

    get alias_base() {
        return this.element.id;
    }

    getAliasedId(alias) {
        if (!this.alias_base) {
            return alias.substr(3);
        }
        return this.alias_base + alias.substr(1);
    }

};