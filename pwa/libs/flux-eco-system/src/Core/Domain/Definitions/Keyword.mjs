export class Keyword {

    static ID = new Keyword("id")
    static API_FILE_PATH = new Keyword("apiFilePath")
    static RECEIVES = new Keyword("receives")
    static SENDS = new Keyword("sends")

    constructor(value) {
        this.value = value
    }

    /**
     * @param {Keyword} definitionKeyword
     * @returns {boolean}
     */
    match(definitionKeyword) {
        return (definitionKeyword.value === this.value);
    }
}