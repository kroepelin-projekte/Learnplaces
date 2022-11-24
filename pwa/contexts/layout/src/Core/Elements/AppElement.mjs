export default class AppElement {
  /**
   * @var {ShadowRoot}
   */
  #shadowRoot;

  /**
   * @private
   */
  constructor() {

  }

  /**
   * @param appName
   * @return {Promise<ShadowRoot>}
   */
  static async initialize(appName) {
    const obj = new AppElement();
    await obj.#createCustomElement(appName);
  }

  /**
   * @return {void}
   */
  async #createCustomElement(appName) {
    const linkStyleSheet = document.getElementById('flux-layout-style');
    const styleElement = document.createElement('style');
    styleElement.innerHTML = await (await fetch(linkStyleSheet.href)).text();
    const tag = appName;
    const applyShadowRootCreated = (shadowRoot) => {
      this.#shadowRoot = shadowRoot;
    }

    customElements.define(
      tag,
      class extends HTMLElement {
        constructor() {
          super();
          const shadowRoot = this.attachShadow({ mode: "open" });
          applyShadowRootCreated(shadowRoot);
          shadowRoot.append(styleElement);
        }

        connectedCallback() {

        }
      }
    );
  }

}
