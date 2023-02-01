export class FluxEcoBroadCastChannel {

    constructor() {
    }

    /**
     * @returns {FluxEcoBroadCastChannel}
     */
    static new() {
        return new this();
    }


    /**
     * @param {ChannelSubscription} channelSubscription
     */
    subscribe(channelSubscription) {
        const channel = new BroadcastChannel(channelSubscription.channelName);
        channel.addEventListener('message', messageEvent => {
            // this.#validateMessage(messageDefinition.payloadSchema, messageEvent.data)
            channelSubscription.operationCallback(messageEvent.data)
        })
    }

    dispatch(message) {
        const channel = new BroadcastChannel(message.header.channelName);
        channel.postMessage(message);
    }

    #validateMessage(messagePayloadSchema, broadcastMessage) {
        //todo
        return true;
    }
}