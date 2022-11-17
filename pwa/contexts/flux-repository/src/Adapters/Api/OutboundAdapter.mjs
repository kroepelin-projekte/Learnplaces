import FluxMessageStreamApi
  from '../../../../flux-message-stream/src/Adapters/Api/FluxMessageStreamApi.mjs';

export default class OutboundAdapter {

  #logEnabled = true;
  #messageStreamApi;
  /** @var string */
  #behaviorsDirectoryPath;
  /** @var string */
  baseUrl;


  /**
   * @param {{behaviorsDirectoryPath: string, baseUrl: string}} payload
   */
  static initialize(payload) {
    return new this(payload)
  }

  /**
   * @private
   */
  constructor(payload) {
    this.#behaviorsDirectoryPath = payload.behaviorsDirectoryPath;
    this.#messageStreamApi =  FluxMessageStreamApi.initialize(this.#logEnabled)
    this.baseUrl = payload.baseUrl;
  }

  async getApiBehaviors() {
    return await this.#importJsonSchema(this.#behaviorsDirectoryPath + '/api/api.json');
  }

  disableLog() {
    this.#logEnabled = false;
    this.#messageStreamApi.logEnabled = false;
  }

  eventStream(actorAddress) {
    return  this.#messageStreamApi.onEvent(actorAddress)
  }

  onRegister(name) {
    return this.#messageStreamApi.onRegister(name + "/repository")
  }


  async #importJsonSchema(src) {
    const templateFilePath = this.#appendBaserUrl(src)
    const response =  await (await fetch(templateFilePath, { assert: { type: 'json' } }));
    return await response.json();
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