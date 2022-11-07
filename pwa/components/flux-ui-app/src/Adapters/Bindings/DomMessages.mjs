export default class DomMessages {

  static new() {
    return new this();
  }

  constructor() {

  }


  onDomContentLoaded(onMessage) {
    window.addEventListener('DOMContentLoaded', (message) => {
        onMessage(message)
    });
  }
}