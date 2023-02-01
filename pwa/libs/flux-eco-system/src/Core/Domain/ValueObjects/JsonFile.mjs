// Season enums can be grouped as static members of a class
export class JsonFile {
    /**
     * @var {string}
     */
    filePath


    constructor(filePath) {
        this.filePath = filePath;
    }


    static new(filePath) {
        return new this(filePath)
    }

    /**
     * @returns {Promise<any>}
     */
    async toJson() {
        const jsonFile = await fetch(this.filePath);
        return await jsonFile.json();
    }
}