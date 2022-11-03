import { ContextInitialized, Created } from './DomainEvents.mjs';
import { InitializeLayoutContext } from '../Ports/Commands.mjs';

export default class Aggregate {
  /** @param {string} */
  appId;

  /**  @var {DomainEventPublisher} */
  #domainEventPublisher;

  /** @var {array} */
  #components = []

  /**
   * @param {string} appId
   * @param {DomainEventPublisher} domainEventPublisher
   * @return {Aggregate}
   */
  static create(appId, domainEventPublisher) {
    return new this(appId, domainEventPublisher);
  }

  /**
   * @param {string} appId
   * @param {DomainEventPublisher} domainEventPublisher
   */
  constructor(appId, domainEventPublisher) {
    this.appId = appId;
    this.#domainEventPublisher = domainEventPublisher;
    this.#applyCreated(Created.new(appId));
  }

  /**
   * @param {Created} event
   */
  #applyCreated(event) {
    //this.shadowRoot = document.head.attachShadow({ mode: "closed" });
    this.#publish(event.name, event)
  }

  /**
   * @param {InitializeLayoutContext} command
   * @return {void}
   */
  async initializeLayoutContext(command) {
    await this.#applyContextInitialized(ContextInitialized.new('layout', command.srcApi))
  }

  /**
   * @param {ContextInitialized} event
   */
  async #applyContextInitialized(event) {
    const ApiClass = await (await import(event.srcApi)).default;
    this.#components[event.contextId] = ApiClass.create(event);
    this.#publish(ContextInitialized.name, event);
  }

  /**
   * @param {string} eventName
   * @param {DomainEvent} event
   * @return {void}
   */
  #publish(eventName, event) {
    this.#domainEventPublisher.publish(eventName, event)
  }
}