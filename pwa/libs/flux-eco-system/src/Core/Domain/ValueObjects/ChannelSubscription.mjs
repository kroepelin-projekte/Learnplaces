export class ChannelSubscription {
    /**
     * @var {String}
     */
    channelName;
    /**
     * @var {Map.<string,{BindingDefinition}>}
     */
    bindingDefinition;
    /**
     * @var {Function}
     */
    operationCallback;

    /**
     * @param {String} channelName
     * @param {Map} bindingDefinition
     * @param {Function} operationCallback
     */
    constructor(channelName, bindingDefinition, operationCallback) {
        this.channelName = channelName;
        this.bindingDefinition = bindingDefinition;
        this.operationCallback = operationCallback;
    }

    /**
     * @param {String} channelName
     * @param {Map} bindingDefinition
     * @param {Function} operationCallback
     * @returns {ChannelSubscription}
     */
    static new(channelName, bindingDefinition, operationCallback) {
        return new this(channelName, bindingDefinition, operationCallback);
    }


}
