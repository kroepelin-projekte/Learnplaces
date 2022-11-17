import FluxMessageStreamApi
  from '../../../../flux-message-stream/src/Adapters/Api/FluxMessageStreamApi.mjs';
import FluxLayoutComponentApi
  from '../../../../flux-layout-component/src/Adapters/Api/FluxLayoutComponentApi.mjs';
import FluxRepositoryApi from '../../../../flux-repository/src/Adapters/Api/FluxRepositoryApi.mjs';

export default class OutboundAdapter {

  #logEnabled = true;
  #messageStreamApi;

  static new() {
    return new this();
  }

  /**
   * @private
   */
  constructor() {
    this.#messageStreamApi = FluxMessageStreamApi.initialize(this.#logEnabled)
  }

  disableLog() {
    this.#logEnabled = false;
    this.#messageStreamApi.logEnabled = false;
  }

  commandStream(actorAddress) {
    return  this.#messageStreamApi.onCommand(actorAddress)
  }

  async getApiBehaviors() {
    return await this.#importJsonSchema('./behaviors/api/api.json');
  }

  async #importJsonSchema(src) {
    const templateFilePath = this.#appendBaserUrl(src)
    const response =  await (await fetch(templateFilePath, { assert: { type: 'json' } }));
    return await response.json();
  }

  eventStream(actorAddress) {
    return  this.#messageStreamApi.onEvent(actorAddress)
  }

  /**
   * @return {(function(message): void)}
   */
  onEvent() {
    return this.#messageStreamApi.onEvent('flux-app')
  }

  /**
   * @return {(function(message): void)}
   */
  publish(address, payload) {
    return this.#messageStreamApi.onEvent('flux-app')
  }

  onRegister(name) {
    return this.#messageStreamApi.onRegister(name)
  }

  /**
   * @param  {{name: string}} payload
   * @return {void}
   */
  initializeLayoutComponent(payload) {
    FluxLayoutComponentApi.initialize(payload)
  }

  /**
   * @param {{name: string}} payload
   * @return {void}
   */
  initializeRepositories(payload) {
    FluxRepositoryApi.initialize(payload)
  }

  /**
   * @param {string} src
   */
  async importJsonSchema(src) {
    const url = this.#appendBaserUrl(src);
    const schema = await (await fetch(url));
    return await schema.json();
  }

  /**
   * @param {string} src
   * @return {string}
   */
  #appendBaserUrl(src) {
    const baseUrl = document.getElementById("flux-pwa-base");
    return baseUrl.href + "/" + src;
  }
}

