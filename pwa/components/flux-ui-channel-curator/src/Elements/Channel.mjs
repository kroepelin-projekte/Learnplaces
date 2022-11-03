export default class Channel {

  /**
   * @typedef {{channelId: string, address: string}} ChannelPayload
   * @var {ChannelPayload}
   */
  payload;

  /** @var {string[]} */
  eventListeners = [];


  /**
   * @private
   * @param {ChannelPayload} payload
   */
  static new(payload) {
    return new this(payload);
  }

  /**
   * @private
   */
  constructor(payload) {
    this.payload = payload;
  }
}


