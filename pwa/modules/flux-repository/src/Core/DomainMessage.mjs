export default class DomainMessage {
  /**
   * @param {{name: string}} payload
   * @return {{name: string}} payload
   */
  static initialized( payload ) {
    return payload
  }

  /**
   * @param {{name: string}} payload
   * @return {{name: string}} payload
   */
  static defined(payload ) {
    return payload
  }

  /**
   * @param {{name: string, id: string}} payload
   * @return {{name: string, id: string}} payload
   */
  static created( payload ) {
    return payload
  }
}