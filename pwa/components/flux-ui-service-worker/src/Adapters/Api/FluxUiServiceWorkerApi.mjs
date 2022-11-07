export default class FluxUiServiceWorkerApi {

  static create() {
    return new this()
  }

  constructor() {
    this.register();
  }

  async register() {
    try {
      await navigator.serviceWorker.register("/Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/pwa/serviceworker.mjs", {
        type: "module"
      });
    } catch (error) {
      console.error(error);
    }
  }
}