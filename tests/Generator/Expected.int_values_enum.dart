import 'package:collection/collection.dart';
enum IntValuesEnum {
  none(0),
  warning(10),
  error(20);

  final String value;
  const IntValuesEnum(this.value);

  factory IntValuesEnum.fromValue(String value) {
    final enumValue = IntValuesEnum.values.firstWhereOrNull(
      (e) => e.value == value,
    );
    if (enumValue == null) {
      throw "ENUM IntValuesEnum HAS NO MAPPING FOR VALUE $value";
    }
    return enumValue;
  }
}
