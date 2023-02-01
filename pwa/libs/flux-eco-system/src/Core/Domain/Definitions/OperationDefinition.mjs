export class OperationDefinition {
    /**
     * @var {string}
     */
    name
    /**
     * @var {object}
     */
    payloadSchema

    constructor(name, payloadSchema) {
        this.name = name
        this.payload = payloadSchema
    }

    static new(name, payloadSchema) {
        return new this(name, payloadSchema)
    }

    static fromJson(json) {
        return new this(json.name, json.payloadSchema)
    }
}