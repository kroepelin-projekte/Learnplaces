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
        console.log('%c' + actor,'color:green');
        console.log('has registered a reactor. He will react in future on messages send to:')
        console.log('%c' + address,'color:green');
        console.groupEnd()
      }
    }
  }

  /**
   * @param payload
   * @param address
   */
  publish(payload, address) {
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
   * @param {string} actor
   * @return {(function(address, payload): void)}
   */
  onEvent(actor) {
    /**
     * @param {string} address
     * @param {object} payload
     * @return void;
     */
    return (address, payload) => {
      const message = {
        "headers": {
          "address": address,
        },
        "payload": payload
      }

      if (this.logEnabled === true) {
        console.group()
        console.log('Actor');
        console.log('%c' + actor,'color:blue');
        console.log('has published')
        console.log('%c' +  JSON.stringify(payload),'color:blue');
        console.log('to')
        console.log('%c' +  address,'color:blue');
        console.groupEnd()
      }

      const channel = new BroadcastChannel(address)
      channel.postMessage(message)
    }
  }
}