export class Pointer {
    /**
     * @var {object}
     */
    resourceObject
    /**
     * @var {string}
     */
    valuePath


    constructor(resourceObject, valuePath) {
        this.resourceObject = resourceObject;
        this.valuePath = valuePath;
    }


    static new(resourceObject, valuePath) {
        return new this(resourceObject, valuePath)
    }

    resolve() {
        const pathStrSplit = this.valuePath.split('/');
        let objectValue = this.resourceObject;
        pathStrSplit.forEach((objectKey, index) => {
            objectValue = this.#extractValue(objectValue, objectKey)
        });
        return objectValue;
    }

    #extractValue(object, key) {
        if (object.hasOwnProperty(key)) {
            return object[key]
        }
    }
}