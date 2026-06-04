import 'package:uuid/uuid_value.dart';
import '/model/dayploy/dart_dto_bundle/tests/src/entity/my_class.dart';

class ForeignClass {
  late final UuidValue id;
  late final MyClass? myClass;

  ForeignClass({
    required this.id,
    this.myClass,
  });

  ForeignClass.construct();

  factory ForeignClass.fromJson(Map<String, dynamic> json) {
    final entity = ForeignClass.construct();

    if (json.containsKey('id')) {
      entity.id = UuidValue.fromString(json['id'] as String);
    }

    if (json.containsKey('myClass')) {
      entity.myClass = json['myClass'] != null ? MyClass.fromJson(json['myClass'] as Map<String, dynamic>) : null;
    }

    return entity;
  }

  Map<String, dynamic> toJson() {
    return {
      "id": id.toString(),
      "myClass": myClass.toJson()?,
    };
  }
}
