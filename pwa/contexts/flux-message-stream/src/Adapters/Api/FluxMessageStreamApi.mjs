/** @typedef {{ name: string, payload: Object }} Message */

export default class FluxMessageStreamApi {

    logEnabled = false;

    /**
     * @return {FluxMessageStreamApi}
     */
    static async new(logEnabled = false) {
        return new this(logEnabled)
    }

    constructor(logEnabled) {
        this.logEnabled = logEnabled;
    }

    publish(actorName,address,payload) {
        if (this.logEnabled === true) {
            console.group()
            console.log('Actor');
            console.log('%c' + actorName, 'color:blue');
            console.log('has published')
            console.log('%c' + JSON.stringify(payload), 'color:blue');
            console.log('to')
            console.log('%c' + address, 'color:blue');
            console.groupEnd()
        }

        const channel = new BroadcastChannel(address)
        channel.postMessage(payload)
    }

    register(actorName,address,onMessage) {
        const channel = new BroadcastChannel(address);
        channel.addEventListener('message', messageEvent => {
            onMessage(messageEvent.data);
        })

        if (this.logEnabled === true) {
            console.group()
            console.log('Actor');
            console.log('%c' + actorName, 'color:green');
            console.log('has registered a reactor. He will react in future on messages send to:')
            console.log('%c' + address, 'color:green');
            console.groupEnd()
        }
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
     * @param actorAddress
     * @param messageAddress
     * @param payload
     */
    onCommand(actorAddress) {
        /**
         * @param {Message} message
         * @return void;
         */
        return (address, payload) => {

            if (this.logEnabled === true) {
                console.group()
                console.log('Actor');
                console.log('%c' + actorAddress, 'color:blue');
                console.log('has published')
                console.log('%c' + JSON.stringify(payload), 'color:blue');
                console.log('to')
                console.log('%c' + address, 'color:blue');
                console.groupEnd()
            }

            const channel = new BroadcastChannel(address)
            channel.postMessage(payload)
        }
    }


    /**
     * @return {(function(message): void)}
     */
    onEvent(actorAddress) {
        /**
         * @param {Message} message
         * @return void;
         */
        return (messageName, payload) => {
            const address = actorAddress + "/" + messageName;

            if (this.logEnabled === true) {
                console.group()
                console.log('Actor');
                console.log('%c' + actorAddress, 'color:blue');
                console.log('has published')
                console.log('%c' + JSON.stringify(payload), 'color:blue');
                console.log('to')
                console.log('%c' + address, 'color:blue');
                console.groupEnd()
            }

            const channel = new BroadcastChannel(address)
            channel.postMessage(payload)
        }
    }
}