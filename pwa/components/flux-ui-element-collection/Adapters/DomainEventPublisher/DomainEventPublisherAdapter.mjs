import DomainEventPublisher from '../../Core/Ports/DomainEventPublisher.mjs';
import { FluxUiMenuEvent } from '../../Core/Domain/DomainEvents.mjs';

class DomainEventPublisherAdapter extends DomainEventPublisher {

  /**
   * @var {array}
   */
  #eventListeners;

  /**
   * @param {string} eventName
   * @param {DomainEvent} event
   */
  publish(eventName, event)  {
    this.#eventListeners[eventName].forEach(listener => {
      listener(event);
    })
  }

  /**
   * @param eventName
   * @param {EventListenerOrEventListenerObject} listener
   */
  addEventListener(eventName, listener) {
      this.#eventListeners[eventName] = [...listener]
  }

}