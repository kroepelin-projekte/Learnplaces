import DomainEventPublisherAdapter from '../DomainEventPublisher/DomainEventPublisherAdapter.mjs';

/**
 * @type {{maintainerId, domainEventPublisher: DomainEventPublisher, componentId: string, AppId, contextKeeperApiSrc: string}}
 */
export default class Configs {
  /**
   * @param maintainerId
   * @param AppId
   */
  static create(maintainerId, AppId) {
    return {
      componentId: maintainerId + "/" + AppId,
      maintainerId: maintainerId,
      AppId: AppId,
      //todo
      contextKeeperApiSrc: "/Customizing/global/plugins/Services/Repository/RepositoryObject/Learnplaces/pwa/components/flux-ui-context-keeper/Adapters/Api/FluxUiContextKeeperApi.mjs",
      domainEventPublisher: DomainEventPublisherAdapter.new()
    }
  }
}
