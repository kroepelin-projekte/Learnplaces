import FluxMessageStreamApi
  from '../../../../flux-message-stream/src/Adapters/Api/FluxMessageStreamApi.mjs';

export default class MessageStream {

  /**
   * @var {string}
   */
  #actorName;

  /**
   * @var {FluxMessageStreamApi}
   */
  #messageStream;


  /** @var private */
  constructor(actorName) {
    this.#actorName = actorName;
  }

  static async new(actorName, logEnabled) {
    const obj = new this(actorName);
    obj.#messageStream = await FluxMessageStreamApi.new(logEnabled);
    return obj;
  }

  publish(
    address,
    payload
  ) {
    this.#messageStream.publish(
      this.#actorName,
      address,
      payload
    )
  }

  register(
    address,
    onMessage
  ) {
    this.#messageStream.register(
      this.#actorName,
      address,
      onMessage
    )
  }
}