<?php echo '<?xml version="1.0" encoding="utf-8" standalone="no"?>' ?><VoidedDocuments xmlns="urn:sunat:names:specification:ubl:peru:schema:xsd:VoidedDocuments-1" 
                xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" 
                xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" 
                xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" 
                xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1" 
                xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">                 
<ext:UBLExtensions>
<ext:UBLExtension>
<ext:ExtensionContent></ext:ExtensionContent>
</ext:UBLExtension>
<ext:UBLExtension>
    <ext:ExtensionContent>
    </ext:ExtensionContent>
  </ext:UBLExtension>
</ext:UBLExtensions>        
 <cbc:UBLVersionID><?php echo $ublVersionIdSwf; ?></cbc:UBLVersionID>
 <cbc:CustomizationID><?php echo $CustomizationIdSwf; ?></cbc:CustomizationID>
 <cbc:ID><?php echo $idComunicacion; ?></cbc:ID>
 <cbc:ReferenceDate><?php echo $fechaDocumentoBaja; ?></cbc:ReferenceDate>
 <cbc:IssueDate><?php echo $fechaComunicacioBaja; ?></cbc:IssueDate>
 <cac:Signature>
    <cbc:ID><?php echo $nroRucEmisorSwf; ?></cbc:ID>
    <cbc:Note><?php echo $identificadorFacturadorSwf; ?></cbc:Note>
    <cbc:ValidatorID><?php echo $codigoFacturadorSwf; ?></cbc:ValidatorID>
    <cac:SignatoryParty>
        <cac:PartyIdentification>
          <cbc:ID><?php echo $nroRucEmisorSwf; ?></cbc:ID>
        </cac:PartyIdentification>
        <cac:PartyName>
          <cbc:Name><?php echo $nombreComercialSwf; ?></cbc:Name>
        </cac:PartyName>
        <cac:AgentParty>
          <cac:PartyIdentification>
            <cbc:ID><?php echo $nroRucEmisorSwf; ?></cbc:ID>
          </cac:PartyIdentification>
          <cac:PartyName>
            <cbc:Name><?php echo $razonSocialSwf; ?></cbc:Name>
          </cac:PartyName>
          <cac:PartyLegalEntity>
            <cbc:RegistrationName><?php echo $razonSocialSwf; ?></cbc:RegistrationName>
          </cac:PartyLegalEntity>
        </cac:AgentParty>
      </cac:SignatoryParty>
      <cac:DigitalSignatureAttachment>
        <cac:ExternalReference>
          <cbc:URI><?php echo $identificadorFirmaSwf; ?></cbc:URI>
        </cac:ExternalReference>
      </cac:DigitalSignatureAttachment>
 </cac:Signature>
  
 <cac:AccountingSupplierParty>
  <cbc:CustomerAssignedAccountID><?php echo $nroRucEmisorSwf; ?></cbc:CustomerAssignedAccountID>
  <cbc:AdditionalAccountID><?php echo $tipDocuEmisorSwf; ?></cbc:AdditionalAccountID>
  <cac:Party>
    <cac:PartyLegalEntity>
      <cbc:RegistrationName><?php echo $razonSocialSwf; ?></cbc:RegistrationName>
    </cac:PartyLegalEntity>
  </cac:Party>
  </cac:AccountingSupplierParty>
  <?php foreach($listaResumen as $resumen): ?>
  <sac:VoidedDocumentsLine>
    <cbc:LineID><?php echo $resumen['linea']; ?></cbc:LineID>
    <cbc:DocumentTypeCode><?php echo $resumen['tipoDocumentoBaja']; ?></cbc:DocumentTypeCode>
    <sac:DocumentSerialID><?php echo $resumen['serieDocumentoBaja']; ?></sac:DocumentSerialID>
    <sac:DocumentNumberID><?php echo $resumen['nroDocumentoBaja']; ?></sac:DocumentNumberID>
    <sac:VoidReasonDescription><?php echo $resumen['motivoBajaDocumento']; ?></sac:VoidReasonDescription>
  </sac:VoidedDocumentsLine>
  <?php endforeach; ?>
</VoidedDocuments>