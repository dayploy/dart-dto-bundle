import 'package:uuid/uuid_value.dart';
import '/model/dayploy/dart_dto_bundle/tests/src/entity/foreign_class.dart';
import '/model/dayploy/dart_dto_bundle/tests/src/entity/int_values_enum.dart';
import '/model/dayploy/dart_dto_bundle/tests/src/entity/string_values_enum.dart';

class MyClass {
  late final UuidValue id;
  late final int numberInt;
  late final double numberFloat;
  late final DateTime maDate;
  late final String name;
  late final String? nullableString;
  late final List<ForeignClass> foreignClasses;
  late final List<int> references;
  late final IntValuesEnum intEnum;
  late final StringValuesEnum stringEnum;

  MyClass({
    required this.id,
    required this.numberInt,
    required this.numberFloat,
    required this.maDate,
    required this.name,
    this.nullableString,
    required this.foreignClasses,
    required this.references,
    required this.intEnum,
    required this.stringEnum,
  });

}
