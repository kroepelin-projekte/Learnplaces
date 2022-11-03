export default class DomainEventPublisher {
  /**
   * @param {string} eventName
   * @param {DomainEvent} event
   */
  publish(eventName, event) {
    console.error("publish() method not implemented");
  }

  /**
   * @param {string} eventName
   * @param {function} listener
   * @abstract
   */
  addEventListener(eventName, listener) {
  }
}
