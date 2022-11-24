/**
 * @typedef {{name: string, projectCurrentUserAddress: string }} CreatedEvent
 */

/**
 * @param {string} name
 * @param {string} projectCurrentUserAddress
 * @return {CreatedEvent}
 */
export const created = function (name, projectCurrentUserAddress) {
  return { name: name, projectCurrentUserAddress: projectCurrentUserAddress }
}