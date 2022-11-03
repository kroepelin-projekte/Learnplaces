

export default class FluxUiContextKeeperConfigs {
  /**
   * @param contextId
   */
  static create(contextId) {
    return {
      contextId: contextId,
      //domainEventPublisher: DomainEventPublisherAdapter.new(),
      uiElementApiSrc: "/Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/pwa/components/flux-ui-element/src/Adapters/Api/FluxUiElementApi.mjs",
    }
  }
}
