/**
 * @typedef {{projection: string, next: {command: string, commandPayload: {templateName: string, parentId: string}}}} FetchData
 * @typedef {{name: string}} CreatedEvent
 * @typedef {{command: string, commandPayload: {templateName: string, parentId: string, data: Object}} }DataChangedEvent
 */


/**
 * @param {string} name
 * @return {CreatedEvent}
 */
export const created = function (name) {
  return { name: name }
}

/**
 * @return {DataChangedEvent}
 */
export const dataChanged = function (command, commandPayload) {
  return { command: command, commandPayload: commandPayload }
}