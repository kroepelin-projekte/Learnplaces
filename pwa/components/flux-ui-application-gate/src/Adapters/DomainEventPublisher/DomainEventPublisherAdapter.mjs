import DomainEventPublisher from '../../Core/Ports/DomainEventPublisher.mjs';
import { DomainEvent } from '../../Core/Domain/DomainEvents.mjs';

export default class DomainEventPublisherAdapter extends DomainEventPublisher {

  /**
   * @var {}
   */
  #eventListeners = {}

  /**
   * @return DomainEventPublisher
   */
  static new() {
    return new this();
  }

  constructor() {
    super()
  }

  /**
   * @param {string} eventName
   * @param {DomainEvent} event
   */
  publish(eventName, event)  {
    if(this.#eventListeners.hasOwnProperty(eventName)) {
      this.#eventListeners[eventName].forEach(listener => {
        listener(event);
      })
    }

  }

  /**
   * @param {string} eventName
   * @param {function} listener
   */
  addEventListener(eventName, listener) {
      this.#eventListeners.eventName = [...listener]
  }

}