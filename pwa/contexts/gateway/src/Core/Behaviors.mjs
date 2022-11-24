/**
 * @typedef {{name: string}} CreatedEvent
 */

/**
 * @param {string} name
 * @return {CreatedEvent}
 */
export const created = function (name) {
  return { name: name }
}