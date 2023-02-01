export class ChannelDefinition {
    /**
     * @var {String}
     */
    address
    /**
     * @var {Message}
     */
    message
    /**
     * @var {Map.<string,{Binding}>}
     */
    bindings;


    /**
     * @param {String} address
     * @param {Message} message
     * @param {Map} bindings
     */
    constructor(address, message, bindings) {
        this.address = address
        this.message = message
        this.bindings = bindings
    }

    /**
     * @param address
     * @param message
     * @param bindings
     */
    static new(address, message, bindings) {
        return new this(address, message, bindings)
    }

    static fromJson(json) {
        return new this(json.path, json.bindingTypes, json.messageHeaderMapping, json.header, json.content)
    }

}