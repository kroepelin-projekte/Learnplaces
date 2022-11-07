import FluxBroadcastChannelApi
  from '../../../../flux-ui-broadcast-channel/src/Adapters/Api/FluxBroadcastChannelApi.mjs';
import Aggregate from '../../Core/Domain/Aggregate.mjs';
import domainMessage from '../../Core/Domain/DomainMessage.mjs';
import artefact from '../../Core/Domain/Artefact.mjs';

export default class FluxUiProjectionApi {

  /**
   * @var {string}
   */
  tagName;
  /**
   * @var {BroadcastChannelBinding}
   */
  #broadcastChannelBinding;

  #aggregate;


  /**
   * @return {FluxUiProjectionApi}
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
    this.#broadcastChannelBinding.addListener(
      domainMessage.slotchanged,
      (message) => this.#onSlotChanged(message.data)
    )
  }

  /**
   * @param message
   */
  #onSlotChanged(message) {
    const payload = message.payload
    const slotChangedReactors = [];
    slotChangedReactors[artefact.fluxAppElementArtefact.slotNameComponentApi] = (changedSlot) => this.#aggregate.initComponent(changedSlot.data)

    slotChangedReactors[payload.name](payload)
  }

  #initAggregate() {
    this.#aggregate = Aggregate.create(this.tagName, this.#broadcastChannelBinding.publishCallback(true));
  }
}