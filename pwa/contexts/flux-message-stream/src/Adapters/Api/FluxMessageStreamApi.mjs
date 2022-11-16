/** @typedef {{ name: string, payload: Object }} Message */

export default class FluxMessageStreamApi {

    logEnabled = false;

    /**
     * @return {FluxMessageStreamApi}
     */
    static initialize(logEnabled = false) {
        return new this(logEnabled)
    }

    constructor(logEnabled) {
        this.logEnabled = logEnabled;
    }

    onRegister(actor) {
        return (
            address,
            onMessage
        ) => {
            const channel = new BroadcastChannel(address)
            channel.addEventListener('message', messageEvent => {
                onMessage(messageEvent.data);
            })
            if (this.logEnabled === true) {
                console.group()
                console.log('Actor');
                console.log('%c' + actor, 'color:green');
                console.log('has registered a reactor. He will react in future on messages send to:')
                console.log('%c' + address, 'color:green');
                console.groupEnd()
            }
        }
    }

    /**
     * @param payload
     * @param address
     */
    publish(from, to, messageName, payload) {

        if (this.logEnabled === true) {
            console.group()
            console.log('Actor');
            console.log('%c' + from, 'color:blue');
            console.log('has published')
            console.log('%c' + messageName + ":", 'color:blue');
            console.log('%c' + JSON.stringify(payload), 'color:blue');
            console.log('to')
            console.log('%c' + to, 'color:blue');
            console.groupEnd()
        }

        const address = to + "/" + messageName;
        const channel = new BroadcastChannel(address);
        const message = {
            "headers": {
                "address": address,
            },
            "payload": payload
        }
        channel.postMessage(message);
    }


    /**
     * @return {(function(message): void)}
     */
    onEvent(actor) {
        /**
         * @param {Message} message
         * @return void;
         */
        return (message) => {
            const address = actor + "/" + message.name;

            if (this.logEnabled === true) {
                console.group()
                console.log('Actor');
                console.log('%c' + actor, 'color:blue');
                console.log('has published')
                console.log('%c' + JSON.stringify(message.payload), 'color:blue');
                console.log('to')
                console.log('%c' + address, 'color:blue');
                console.groupEnd()
            }

            const channel = new BroadcastChannel(address)
            channel.postMessage(message)
        }
    }
}