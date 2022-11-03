import Aggregate from '../Domain/Aggregate.mjs';
import { InitializeLayoutContext, Create } from './Commands.mjs';
import DomainEventPublisherAdapter
  from '../../Adapters/DomainEventPublisher/DomainEventPublisherAdapter.mjs';

export default class Service {
  /**
   * @var {Aggregate}
   */
  #aggregate;

  /**
   * @var {DomainEventPublisher}
   */
  #domainEventPublisher

  /**
   * @return Service
   */
  static new(domainEventPublisherAdapter) {
    return new this(domainEventPublisherAdapter);
  }

  constructor(domainEventPublisherAdapter) {
    this.#domainEventPublisher = domainEventPublisherAdapter;
  }

  /**
   * @param {Create} payload
   */
  create(payload) {
    this.#aggregate = Aggregate.create(payload.componentId, this.#domainEventPublisher);
  }

  /**
   * @param {InitializeLayoutContext} command
   * @return {void}
   */
  initializeLayoutContext(command) {
      this.#aggregate.initializeLayoutContext(command)
  }

}