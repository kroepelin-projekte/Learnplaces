import Channel from './Channel.mjs';

export default class Reactor {

  /**
   * @typedef {{ reactorId: string, channel: Channel, bindingTypes: ReactorBinding[]}} ReactorPayload
   * @var ReactorPayload
   */
  payload;


  /**
   * @param {ReactorPayload} payload
   */
  static new(payload) {
    return new this(payload);
  }

  /**
   * @param {ReactorPayload} payload
   */
  constructor(payload) {
    this.payload = payload;
  }


}