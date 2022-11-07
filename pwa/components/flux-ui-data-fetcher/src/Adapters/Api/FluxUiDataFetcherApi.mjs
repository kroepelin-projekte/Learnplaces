import FluxBroadcastChannelApi
  from '../../../../flux-ui-broadcast-channel/src/Adapters/Api/FluxBroadcastChannelApi.mjs';
import Aggregate from '../../Core/Domain/Aggregate.mjs';

export default class FluxUiDataFetcherApi {

  /**
   * @var {string}
   */
  tagName;
  /**
   * @var {BroadcastChannelBinding}
   */
  #broadcastChannelBinding;


  /**
   * @return {FluxUiDataFetcherApi}
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
    this.#broadcastChannelBinding.addListener('fetchData',
      (messageEvent) => this.#onFetchData(messageEvent.data));
  }

  #onFetchData(payload) {
      const id = payload.id;
      const aggregate = Aggregate.fromId(id,  this.tagName,   this.#broadcastChannelBinding);

  }

  #initAggregate() {
      Aggregate.create(this.tagName, this.#broadcastChannelBinding.publishCallback(true));
  }
}