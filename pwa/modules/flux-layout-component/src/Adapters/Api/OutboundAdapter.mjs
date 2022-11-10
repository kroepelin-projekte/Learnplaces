import FluxMessageStreamApi
  from '../../../../flux-message-stream/src/Adapters/Api/FluxMessageStreamApi.mjs';

export default class OutboundAdapter {

  #logEnabled = true;
  #messageStreamApi;
  behaviorsDirectory;


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
    this.behaviorsDirectory = 'modules/flux-layout-component/behaviors';
    this.#messageStreamApi =  FluxMessageStreamApi.initialize(this.#logEnabled)
  }

  async getBehaviors() {
    return await this.#importJsonSchema('./modules/flux-layout-component/schemas/behaviors/flux-layout-component.asyncapi.json');
  }

  disableLog() {
    this.#logEnabled = false;
    this.#messageStreamApi.logEnabled = false;
  }

  onEvent(name) {
    return  this.#messageStreamApi.onEvent(name)
  }

  onRegister(name) {
    return this.#messageStreamApi.onRegister(name)
  }


  async importCss() {
    const path = this.#appendBaserUrl(this.behaviorsDirectory + "/css/stylesheet.css");
    const response = await (await fetch(path));
    return  await response.text();
  }


  /**
   * @param name
   * @return {Promise<any>}
   */
  async loadTemplate(name) {
    const templateFilePath = this.#appendBaserUrl(this.behaviorsDirectory + "/templates/" + name + "-template.json")
    const response =  await (await fetch(templateFilePath, { assert: { type: 'json' } }));
    return await response.json();
  }

  async #importApiClass(src) {
    const ApiClass = await (await (import(this.#appendBaserUrl(src))));
    return ApiClass.default;
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