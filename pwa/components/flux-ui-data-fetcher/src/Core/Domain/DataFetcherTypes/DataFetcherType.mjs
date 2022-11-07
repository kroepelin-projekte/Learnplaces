import offlineFirst from './OfflineFirst.mjs';

/**
 * @typedef {{templateId: string, tagName: string, template: string, id: string}} FluxDataFetcherType
 */
export const FluxDataFetcherType = {}

const dataFetcherType = [];
dataFetcherType[offlineFirst.tagName] = offlineFirst.template;

export default dataFetcherType;