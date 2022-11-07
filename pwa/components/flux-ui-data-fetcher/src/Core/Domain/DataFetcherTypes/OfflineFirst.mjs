const offlineFirst = {}

offlineFirst.contextId = "flux-data-fetcher";
offlineFirst.id = "flux-data-fetcher-offline-first"; //todo
offlineFirst.tagName = "flux-data-fetcher-offline-first";
offlineFirst.templateId = offlineFirst.tagName + '-template';

/**
 * @type {FluxDataFetcherType}
 */
offlineFirst.template = new class OfflineFirst {
  id = offlineFirst.id;
  tagName = offlineFirst.tagName;
  templateId = offlineFirst.templateId;
  template = '<template id="' + this.templateId + '">' +
    '<slot name="endpoint"></slot>' +
    '</template>';

  constructor() {
  }
}

export default offlineFirst;