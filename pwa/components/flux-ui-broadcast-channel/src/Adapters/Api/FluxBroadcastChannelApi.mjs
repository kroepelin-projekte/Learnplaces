import BroadcastChannelBinding from '../Bindings/BroadcastChannelBinding.mjs';

export default class FluxBroadcastChannelApi {

  /**
   * @return {FluxBroadcastChannelApi}
   */
  static new() {
    return new this()
  }

  constructor() {

  }

  /**
   *
   * @param {string} tagName
   * @return {BroadcastChannelBinding}
   */
  createChannel(tagName) {
    return BroadcastChannelBinding.new(tagName);
  }
}