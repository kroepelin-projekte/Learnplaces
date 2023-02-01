export class ReceiveDefinition {
    /**
     * @var {string}
     */
    channelName;
    /**
     * @var {string}
     */
    operationName;
    /**
     * @var {Map}
     */
    bindings;

    /**
     * @param {String} channelName
     * @param {String} operationName
     * @param {Map} bindings
     */
    constructor(channelName, operationName, bindings) {
        this.channelName = channelName;
        this.operationName = operationName;
        this.bindings = bindings;
    }

    /**
     * @param {String} channelName
     * @param {String} operationName
     * @param {Map} bindings
     */
    static new(channelName, operationName, bindings) {
        return new this(channelName, operationName, bindings);
    }
}
