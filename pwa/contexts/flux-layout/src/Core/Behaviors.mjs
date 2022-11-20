/**
 * @typedef {{templateName: string, parentId: string, slotName: string|null, value: string|null, data: array|null, addOnClickEvent: boolean|null}} AppendTemplateContent
 * @typedef {{id: string, slotNames: string[]|null}} CreatedEvent
 */

/**
 * @param {string} id
 * @param {string[]|null} slotNames
 * @return {CreatedEvent}
 */
export const created = function (id, slotNames = null) {
  return { id: id, slotNames: slotNames }
}