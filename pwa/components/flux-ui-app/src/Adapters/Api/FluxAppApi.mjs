import DomMessages from '../Bindings/DomMessages.mjs';
import Aggregate from '../../Core/Domain/Aggregate.mjs';
import FluxBroadcastChannelApi
  from '../../../../flux-ui-broadcast-channel/src/Adapters/Api/FluxBroadcastChannelApi.mjs';
import domainMessage from '../../Core/Domain/DomainMessage.mjs';
import artefact from '../../Core/Domain/Artefact.mjs';

export default class FluxAppApi {

  /**
   * @var {Aggregate}
   */
  #aggregate;

  /**
   * @var {BroadcastChannelBinding}
   */
  #broadcastChannelBinding;

  /**
   * @return {FluxAppApi}
   */
  static initialize() {
    return new this()
  }

  constructor() {
    const channelApi = FluxBroadcastChannelApi.new();
    this.#broadcastChannelBinding = channelApi.createChannel(artefact.fluxAppElementArtefact.tagName);
    this.#initReactors();
  }

  #initReactors() {
    DomMessages.new().onDomContentLoaded((message) => this.#onDOMContentLoaded(message))
    this.#broadcastChannelBinding.addListener(
      domainMessage.slotchanged,
      (message) => this.#onSlotChanged(message.data)
    )
  }

  #onDOMContentLoaded(message) {
    this.#aggregate = Aggregate.create(this.#broadcastChannelBinding.publishCallback(true));
  }

  /**
   * @param message
   */
  #onSlotChanged(message) {
    const payload = message.payload
    const slotChangedReactors = [];
    slotChangedReactors[artefact.fluxAppElementArtefact.slotNameComponentApi] = (changedSlot) => this.#aggregate.initComponent(
      changedSlot.data)
    slotChangedReactors[payload.name](payload)
  }

}