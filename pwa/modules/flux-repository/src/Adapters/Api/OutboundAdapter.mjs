import FluxMessageStreamApi
  from '../../../../flux-message-stream/src/Adapters/Api/FluxMessageStreamApi.mjs';

export default class OutboundAdapter {

  #logEnabled = true;
  #messageStreamApi;


  /**
   *
   * @return {OutboundAdapter}
   */
  static new() {
    return new this();
  }

  /**
   * @private
   */
  constructor() {
    this.#messageStreamApi =  FluxMessageStreamApi.initialize(this.#logEnabled)
  }

  disableLog() {
    this.#logEnabled = false;
    this.#messageStreamApi.logEnabled = false;
  }

  onEvent(name) {
    return this.#messageStreamApi.onEvent(name + "/repository")
  }

  onRegister(name) {
    return this.#messageStreamApi.onRegister(name + "/repository")
  }


  async #importJsonSchema(src) {
    const behaviours = await (await (import(this.#appendBaserUrl(src), {
      assert: { type: 'json' }
    })));
    console.log(behaviours);
    const loaded = behaviours.default;
    console.log(loaded);
    return loaded;
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