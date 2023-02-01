export class DomainMessageDispatcher {
    /**
     * @var {Function}
     */
    #subscribeToChannelCallback;

    /**
     * @param {Function} subscribeToChannelCallback
     * @returns {DomainMessageDispatcher}
     */
    constructor(
        subscribeToChannelCallback
    ) {
        this.#subscribeToChannelCallback = subscribeToChannelCallback;
    }


    /**
     * @param {Function} subscribeToChannelCallback
     * @returns {DomainMessageDispatcher}
     */
    static new(subscribeToChannelCallback) {
        return new this(subscribeToChannelCallback);
    }


    /**
     * @param {ChannelSubscription} channelSubscription
     */
    applicationSubscribedToChannel(channelSubscription) {
        this.#subscribeToChannelCallback(channelSubscription);
    }

}