export default class ErrorHandling {
    constructor() {
        this.errors = {};
    }

    has(field) {
        return this.errors.hasOwnProperty(field);
    }

    any() {
        return Object.keys(this.errors).length > 0;
    }

    get(field) {
        if (this.errors[field]) {
            // Return errors as a string with line breaks
            return this.errors[field].join('<br/>');
        }
    }


    set(field, error) {
        this.errors[field] = [error];
    }

    record(errors) {
        this.errors = errors;
    }

    clear(field) {
        delete this.errors[field]
    }
}
