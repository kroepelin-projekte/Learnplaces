import DomainEventPublisherAdapter from '../DomainEventPublisher/DomainEventPublisherAdapter.mjs';

export default class FluxUiElementConfigs {
  /**
   * @param elementId
   */
  static create(elementId) {
    return {
      elementId: elementId,
      domainEventPublisher: DomainEventPublisherAdapter.new()
    }
  }
}
