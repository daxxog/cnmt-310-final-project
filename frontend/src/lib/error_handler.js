export class ErrorHandler {
    constructor() {
        this.errors = [];
        this.markedErrors = {};
    }
    get hasError() {
        return this.errors.length !== 0;
    }
    async addError(errorMessage) {
        this.errors.push(errorMessage);
    }
    isMarked(elementName) {
        return this.markedErrors[elementName] ?? false;
    }
    async markError(elementName) {
        this.markedErrors[elementName] = true;
    }
}
