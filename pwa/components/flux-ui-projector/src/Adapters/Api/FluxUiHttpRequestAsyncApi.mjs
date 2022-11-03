/** this is an automated generated class, related to app learnplaces **/

export default class FluxUiHttpRequestAsyncApi {

  /**
   * @var {Service}
   */
  #service;

  static new() {
    return new this();
  }

  /**
   * @private
   */
  constructor() {

  }

  #initReactors() {
    const channel = new BroadcastChannel("flux-ui-menu/HtmlLayoutRendered")
    channel.addEventListener("message", (event) => {
      console.log(event);
      this.#onFluxUiHtmlLayoutRendered(event)
    });
  }

  #onFluxUiHtmlLayoutRendered(event) {
      this.#service.projectData('CourseMenuData', event.elementId);
  }

  #initPublishers() {
    this.#domainEventPublisher.addEventListener('CourseMenuDataProjected', (payload) => {
        this.#publishDomainEvent('CourseMenuDataProjected', payload)
      }
    );
  }

  #publishDomainEvent(eventName, payload) {
    const publisher = new BroadcastChannel("flux-ui-projector/" + eventName)

    publisher.postMessage({
      "headers": {},
      "payload": {
        "data": dataProjected.payload.data
      }
    })
  }
}