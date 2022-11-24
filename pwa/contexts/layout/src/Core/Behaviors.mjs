/**
 * @typedef {{templateName: string, parentId: string, slotName: string|null, value: string|null, data: array|null, addOnClickEvent: boolean|null}} AppendTemplateContent
 * @typedef {{id: string, slotNames: string[]|null}} CreatedEvent
 * @typedef {{elementContainerId: string, idList: string[]}} SlotDataChanged
 */

/**
 * @param {string} id
 * @param {string[]|null} slotNames
 * @return {CreatedEvent}
 */
export const created = function (id, slotNames = null) {
  return { id: id, slotNames: slotNames }
}

/**
 * @param {string} elementContainerId
 * @param {string[]} idList
 * @return {SlotDataChanged}
 */
export const slotDataChanged = function (elementContainerId, idList) {
  return { elementContainerId: elementContainerId, idList: idList }
}