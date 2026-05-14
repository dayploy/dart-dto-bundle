enum IntValuesEnum {
  none(0),
  warning(10),
  error(20);

  final String value;
  const IntValuesEnum(this.value);

  factory IntValuesEnum.fromValue(String value) {
    return IntValuesEnum.values.firstWhere((e) => e.value == value);
  }
}
