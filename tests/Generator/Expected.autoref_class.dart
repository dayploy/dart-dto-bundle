import 'package:uuid/uuid_value.dart';

class AutorefClass {
  final UuidValue id;
  final AutorefClass? autoref;

  AutorefClass({
    required this.id,
    this.autoref,
  });
}
