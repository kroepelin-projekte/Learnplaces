import Aggregate from '../../Core/Domain/Aggregate.mjs';
import FluxBroadcastChannelApi
  from '../../../../flux-ui-broadcast-channel/src/Adapters/Api/FluxBroadcastChannelApi.mjs';

export default class FluxLayoutItemApi {
  /**
   * @var {string}
   */
  tagName;
  /**
   * @var {BroadcastChannelBinding}
   */
  #broadcastChannelBinding;


  /**
   * @return {FluxLayoutItemApi}
   */
  static initialize(tagName) {
    const channelApi = FluxBroadcastChannelApi.new();
    const obj = new this(tagName, channelApi.createChannel(tagName));
    obj.#initReactors();
    obj.#initAggregate()
    return obj;
  }

  constructor(tagName, broadcastChannelBinding) {
    this.tagName = tagName;
    this.#broadcastChannelBinding = broadcastChannelBinding
  }

  #initReactors() {
  }

  #initAggregate() {
    Aggregate.create(this.tagName, this.#broadcastChannelBinding.publishCallback(true));
  }
}