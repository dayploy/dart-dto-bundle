import 'package:uuid/uuid_value.dart';
import '/model/dayploy/dart_dto_bundle/tests/src/entity/foreign_class.dart';
import '/model/dayploy/dart_dto_bundle/tests/src/entity/int_values_enum.dart';
import '/model/dayploy/dart_dto_bundle/tests/src/entity/string_values_enum.dart';
import '/services/api_date_service.dart';

class MyClass {
  late final UuidValue id;
  late final int numberInt;
  late final double numberFloat;
  late final DateTime maDate;
  late final String name;
  late final String? nullableString;
  late final List<ForeignClass> foreignClasses;
  late final ForeignClass singleForeignClass;
  late final List<int> references;
  late final IntValuesEnum intEnum;
  late final StringValuesEnum stringEnum;
  late final StringValuesEnum? stringEnumNullable;
  late final UuidValue? uuidNullable;
  late final List<ForeignClass> dtoList;
  late final List<UuidValue> uuidList;
  late final List<IntValuesEnum> intEnumList;

  MyClass({
    required this.id,
    required this.numberInt,
    required this.numberFloat,
    required this.maDate,
    required this.name,
    this.nullableString,
    required this.foreignClasses,
    required this.singleForeignClass,
    required this.references,
    required this.intEnum,
    required this.stringEnum,
    this.stringEnumNullable,
    this.uuidNullable,
    required this.dtoList,
    required this.uuidList,
    required this.intEnumList,
  });

  MyClass.construct();

  factory MyClass.fromJson(Map<String, dynamic> json) {
    final entity = MyClass.construct();

    if (json.containsKey('id')) {
      entity.id = UuidValue.fromString(json['id'] as String);
    }

    if (json.containsKey('numberInt')) {
      entity.numberInt = json['numberInt'] as int;
    }

    if (json.containsKey('numberFloat')) {
      entity.numberFloat = json['numberFloat'] as double;
    }

    if (json.containsKey('maDate')) {
      entity.maDate = DateTime.parse(json['maDate'] as String);
    }

    if (json.containsKey('name')) {
      entity.name = json['name'] as String;
    }

    if (json.containsKey('nullableString')) {
      entity.nullableString = json['nullableString'] != null ? json['nullableString'] as String : null;
    }

    if (json.containsKey('foreignClasses')) {
      entity.foreignClasses = (json['foreignClasses'] as List<dynamic>).map((e) => ForeignClass.fromJson(e as Map<String, dynamic>)).toList();
    }

    if (json.containsKey('singleForeignClass')) {
      entity.singleForeignClass = ForeignClass.fromJson(json['singleForeignClass'] as Map<String, dynamic>);
    }

    if (json.containsKey('references')) {
      entity.references = (json['references'] as List<dynamic>).map((e) => e as int).toList();
    }

    if (json.containsKey('intEnum')) {
      entity.intEnum = IntValuesEnum.fromValue(json['intEnum']);
    }

    if (json.containsKey('stringEnum')) {
      entity.stringEnum = StringValuesEnum.fromValue(json['stringEnum']);
    }

    if (json.containsKey('stringEnumNullable')) {
      entity.stringEnumNullable = json['stringEnumNullable'] != null ? StringValuesEnum.fromValue(json['stringEnumNullable']) : null;
    }

    if (json.containsKey('uuidNullable')) {
      entity.uuidNullable = json['uuidNullable'] != null ? UuidValue.fromString(json['uuidNullable'] as String) : null;
    }

    if (json.containsKey('dtoList')) {
      entity.dtoList = (json['dtoList'] as List<dynamic>).map((e) => ForeignClass.fromJson(e as Map<String, dynamic>)).toList();
    }

    if (json.containsKey('uuidList')) {
      entity.uuidList = (json['uuidList'] as List<dynamic>).map((e) => UuidValue.fromString(e as String)).toList();
    }

    if (json.containsKey('intEnumList')) {
      entity.intEnumList = (json['intEnumList'] as List<dynamic>).map((e) => IntValuesEnum.fromValue(e)).toList();
    }

    return entity;
  }

  Map<String, dynamic> toJson() {
    return {
      "id": id.toString(),
      "numberInt": numberInt,
      "numberFloat": numberFloat,
      "maDate": ApiDateService.convertToApi(maDate),
      "name": name,
      "nullableString": nullableString,
      "foreignClasses": foreignClasses.map((e) => e.toJson()).toList(),
      "singleForeignClass": singleForeignClass.toJson(),
      "references": references.map((e) => e).toList(),
      "intEnum": intEnum.value,
      "stringEnum": stringEnum.value,
      "stringEnumNullable": stringEnumNullable?.value,
      "uuidNullable": uuidNullable?.toString(),
      "dtoList": dtoList.map((e) => e.toJson()).toList(),
      "uuidList": uuidList.map((e) => e.toString()).toList(),
      "intEnumList": intEnumList.map((e) => e.value).toList(),
    };
  }
}
