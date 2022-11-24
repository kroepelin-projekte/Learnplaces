import { created } from './Behaviors.mjs';

export default class Actor {

  /**
   * @var {string}
   */
  #name;
  /**
   * @function()
   */
  #publish


  /**
   * @private
   */
  constructor(publish) {
    this.#publish = publish;
  }

  /**
   * @return {Actor}
   */
  static async new(name, publish) {
    const obj = new Actor(publish);
    await obj.#created(created(name))
    return obj;
  }

  /**
   * @param {CreatedEvent} payload
   * @return {void}
   */
  async #created(payload) {
    this.#name = payload.name;
    this.#publish(this.#name, created.name, payload)
  }

  /**
   * @return {void}
   */
  async dispatch(publishTo, payload) {
    this.#publish(publishTo, payload)
  }
}