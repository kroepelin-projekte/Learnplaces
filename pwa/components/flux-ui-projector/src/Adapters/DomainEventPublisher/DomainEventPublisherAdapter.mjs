import DomainEventPublisher from '../../Core/Ports/DomainEventPublisher.mjs';

class DomainEventPublisherAdapter extends DomainEventPublisher{

  /**
   * @var {FluxUiHttpRequestAsyncApi}
   */
  #asyncapi;

  /**
   * @param {string} eventName
   * @param {DomainEvent} event
   */
  publish(eventName, event)  {
     this.#asyncapi[eventName]();
  }

}