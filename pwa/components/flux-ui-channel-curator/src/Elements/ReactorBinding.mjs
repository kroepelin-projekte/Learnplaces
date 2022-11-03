class ReactorBinding {

  /**
   * @typedef {{ address: string, reactorId: string, fellowApi}} ReactorBindingPayload
   */
  reactorBindingPayload;

  /**
   * @function
   */
  init;


  /**
   * @return ReactorBinding
   */
  static broadCastBinding() {
    const obj = new this();
    obj.registerInitializer(
      /** @param {ReactorBindingPayload} reactorBindingPayload */
      (reactorBindingPayload) => {
        const channel = new BroadcastChannel(reactorBindingPayload.address)
        channel.addEventListener("message", (event) => {
          console.log(event);
          reactorBindingPayload.fellowApi[reactorBindingPayload.reactorId](event)
        });
      }
    );
    return obj;
  }

  /**
   * @private
   */
  constructor() {

  }

  registerInitializer(initFunction) {
    this.init = initFunction;
  }

  /**
   * @param {ReactorBindingPayload} reactorbindingPayload
   */
  connect(reactorbindingPayload) {
    this.init(reactorbindingPayload);
  }

}


