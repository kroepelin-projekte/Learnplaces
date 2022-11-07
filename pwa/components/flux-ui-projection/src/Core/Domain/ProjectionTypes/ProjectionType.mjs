import itemList from './ItemList.mjs';

/**
 * @typedef {{templateId: string, tagName: string, template: string, id: string}} FluxProjectionType
 */
export const FluxProjectionType = {}

const projectionType = [];
projectionType[itemList.tagName] = itemList.template;

export default projectionType;