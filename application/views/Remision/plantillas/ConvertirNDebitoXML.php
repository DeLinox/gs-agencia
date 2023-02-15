<?php echo '<?xml version="1.0" encoding="utf-8" standalone="no"?>' ?><DebitNote xmlns="urn:oasis:names:specification:ubl:schema:xsd:DebitNote-2" 
         xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" 
         xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" 
         xmlns:ccts="urn:un:unece:uncefact:documentation:2" 
         xmlns:ds="http://www.w3.org/2000/09/xmldsig#" 
         xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2" 
         xmlns:qdt="urn:oasis:names:specification:ubl:schema:xsd:QualifiedDatatypes-2" 
         xmlns:sac="urn:sunat:names:specification:ubl:peru:schema:xsd:SunatAggregateComponents-1" 
         xmlns:udt="urn:un:unece:uncefact:data:specification:UnqualifiedDataTypesSchemaModule:2">

  <ext:UBLExtensions>
      <ext:UBLExtension>
        <ext:ExtensionContent>
          <sac:AdditionalInformation>
            <sac:AdditionalMonetaryTotal>  
              <cbc:ID><?php echo $codigoMontoOperGravadasSwf; ?></cbc:ID>
              <cbc:PayableAmount currencyID="<?php echo $moneda; ?>"><?php echo $montoOperGravadas; ?></cbc:PayableAmount>
            </sac:AdditionalMonetaryTotal>  
            <sac:AdditionalMonetaryTotal>  
              <cbc:ID><?php echo $codigoMontoOperInafectasSwf; ?></cbc:ID>
              <cbc:PayableAmount currencyID="<?php echo $moneda; ?>"><?php echo $montoOperInafectas; ?></cbc:PayableAmount>
            </sac:AdditionalMonetaryTotal>
            <sac:AdditionalMonetaryTotal>
              <cbc:ID><?php echo $codigoMontoOperExoneradasSwf; ?></cbc:ID>
              <cbc:PayableAmount currencyID="<?php echo $moneda; ?>"><?php echo $montoOperExoneradas; ?></cbc:PayableAmount>
            </sac:AdditionalMonetaryTotal>
            <?php if( es($baseImponiblePercepcion) && es($montoPercepcion) && es($montoTotalSumPercepcion)): ?>
<sac:AdditionalMonetaryTotal>
              <cbc:ID schemeID="<?php echo $codRegiPercepcion; ?>"><?php echo $codigoPercepSwf; ?></cbc:ID>
              <sac:ReferenceAmount currencyID="<?php echo $moneda; ?>"><?php echo $baseImponiblePercepcion; ?></sac:ReferenceAmount>
              <cbc:PayableAmount currencyID="<?php echo $moneda; ?>"><?php echo $montoPercepcion; ?></cbc:PayableAmount>
              <sac:TotalAmount currencyID="<?php echo $moneda; ?>"><?php echo $montoTotalSumPercepcion; ?></sac:TotalAmount>
            </sac:AdditionalMonetaryTotal>
            <?php endif; ?>
            <?php if( es($totalVentaOperGratuita)): ?>
<?php $montoOpeNoOnerosa=$totalVentaOperGratuita; ?>
            <?php if( $montoOpeNoOnerosa>0): ?>
<sac:AdditionalMonetaryTotal>  
              <cbc:ID><?php echo $codigoGratuitoSwf; ?></cbc:ID>
              <cbc:PayableAmount currencyID="<?php echo $moneda; ?>"><?php echo $totalVentaOperGratuita; ?></cbc:PayableAmount>
            </sac:AdditionalMonetaryTotal>   
            <?php endif; ?>
            <?php endif; ?>
            <?php foreach($listaLeyendas as $leyenda): ?>
            <sac:AdditionalProperty>  
              <cbc:ID><?php echo $leyenda['codigo']; ?></cbc:ID>
              <cbc:Value><?php echo $leyenda['descripcion']; ?></cbc:Value>
            </sac:AdditionalProperty>    
            <?php endforeach; ?>
            </sac:AdditionalInformation>  
        </ext:ExtensionContent>
      </ext:UBLExtension>
	  <ext:UBLExtension>
    <ext:ExtensionContent>
    </ext:ExtensionContent>
  </ext:UBLExtension>
  </ext:UBLExtensions>    

  <cbc:UBLVersionID><?php echo $ublVersionIdSwf; ?></cbc:UBLVersionID>
  <cbc:CustomizationID><?php echo $CustomizationIdSwf; ?></cbc:CustomizationID>
  <cbc:ID><?php echo $nroCdpSwf; ?></cbc:ID>
  <cbc:IssueDate><?php echo $fechaEmision; ?></cbc:IssueDate>
  <cbc:DocumentCurrencyCode><?php echo $moneda; ?></cbc:DocumentCurrencyCode>
  <cac:DiscrepancyResponse>
    <cbc:ReferenceID><?php echo $nroDocuModifica; ?></cbc:ReferenceID>
    <cbc:ResponseCode><?php echo $codigoMotivo; ?></cbc:ResponseCode>
    <cbc:Description><?php echo $descripcionMotivo; ?></cbc:Description>
  </cac:DiscrepancyResponse>
  <?php foreach($listaRelacionado as $relacion): ?>
  <?php if( $relacion['indDocuRelacionado']=="3"): ?>
<cac:OrderReference>
      <cbc:ID><?php echo $relacion['nroDocuRelacionado']; ?></cbc:ID>
    </cac:OrderReference>
  <?php endif; ?>
  <?php endforeach; ?>
  <cac:BillingReference>
    <cac:InvoiceDocumentReference>
      <cbc:ID><?php echo $nroDocuModifica; ?></cbc:ID>
      <cbc:DocumentTypeCode><?php echo $tipoDocuModifica; ?></cbc:DocumentTypeCode>
    </cac:InvoiceDocumentReference>
  </cac:BillingReference>
  <?php foreach($listaRelacionado as $relacion): ?>
    <?php if( $relacion['indDocuRelacionado']=="1"): ?>
<cac:DespatchDocumentReference>
        <cbc:ID><?php echo $relacion['nroDocuRelacionado']; ?></cbc:ID>
        <cbc:DocumentTypeCode><?php echo $relacion['tipDocuRelacionado']; ?></cbc:DocumentTypeCode>
      </cac:DespatchDocumentReference>
    <?php endif; ?>
  <?php endforeach; ?>
  <?php foreach($listaRelacionado as $relacion): ?>
    <?php if( $relacion['indDocuRelacionado']=="99"): ?>
<cac:AdditionalDocumentReference>
        <cbc:ID><?php echo $relacion['nroDocuRelacionado']; ?></cbc:ID>
        <cbc:DocumentTypeCode><?php echo $relacion['tipDocuRelacionado']; ?></cbc:DocumentTypeCode>
      </cac:AdditionalDocumentReference>
    <?php endif; ?>
  <?php endforeach; ?>
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
      <cac:PartyName> 
        <cbc:Name><?php echo $nombreComercialSwf; ?></cbc:Name>
      </cac:PartyName>
      <cac:PostalAddress>
        <cbc:ID><?php echo $ubigeoDomFiscalSwf; ?></cbc:ID>
        <cbc:StreetName><?php echo $direccionDomFiscalSwf; ?></cbc:StreetName>
        <cac:Country>
          <cbc:IdentificationCode><?php echo $paisDomFiscalSwf; ?></cbc:IdentificationCode>      
        </cac:Country>
      </cac:PostalAddress>
      <cac:PartyLegalEntity>
        <cbc:RegistrationName><?php echo $razonSocialSwf; ?></cbc:RegistrationName>
      </cac:PartyLegalEntity>      
    </cac:Party>
  </cac:AccountingSupplierParty>
  
  <cac:AccountingCustomerParty>
    <cbc:CustomerAssignedAccountID><?php echo $nroDocuIdenti; ?></cbc:CustomerAssignedAccountID>
    <cbc:AdditionalAccountID><?php echo $tipoDocuIdenti; ?></cbc:AdditionalAccountID>
    <cac:Party>
      <?php if( es($codigoPaisCliente) && es($codigoUbigeoCliente) && es($direccionCliente) && es($direccionCliente)): ?>
<cac:PostalAddress>
        <cbc:ID><?php echo $codigoUbigeoCliente; ?></cbc:ID>
        <cbc:StreetName><?php echo $direccionCliente; ?></cbc:StreetName>
        <cac:Country>
          <cbc:IdentificationCode><?php echo $codigoPaisCliente; ?></cbc:IdentificationCode>
        </cac:Country>
      </cac:PostalAddress>
      <cac:PhysicalLocation>
        <cbc:Description><?php echo $direccionCliente; ?></cbc:Description>
      </cac:PhysicalLocation>
      <?php endif; ?>
      <cac:PartyLegalEntity>
        <cbc:RegistrationName><?php echo $razonSocialUsuario; ?></cbc:RegistrationName>
      </cac:PartyLegalEntity>
    </cac:Party>
  </cac:AccountingCustomerParty>
 
  <cac:TaxTotal>
    <cbc:TaxAmount currencyID="<?php echo $moneda; ?>"><?php echo $sumaIgv; ?></cbc:TaxAmount>
    <cac:TaxSubtotal>
      <cbc:TaxAmount currencyID="<?php echo $moneda; ?>"><?php echo $sumaIgv; ?></cbc:TaxAmount>
      <cac:TaxCategory>
        <cac:TaxScheme>
          <cbc:ID><?php echo $idIgv; ?></cbc:ID>
          <cbc:Name><?php echo $codIgv; ?></cbc:Name>
          <cbc:TaxTypeCode><?php echo $codExtIgv; ?></cbc:TaxTypeCode>
        </cac:TaxScheme>
      </cac:TaxCategory>  
    </cac:TaxSubtotal>
  </cac:TaxTotal>
<?php $sumatoriaIsc=$sumaIsc; ?>
<?php if( ($sumatoriaIsc > 0)): ?>
<cac:TaxTotal>  
    <cbc:TaxAmount currencyID="<?php echo $moneda; ?>"><?php echo $sumaIsc; ?></cbc:TaxAmount>
    <cac:TaxSubtotal>
      <cbc:TaxAmount currencyID="<?php echo $moneda; ?>"><?php echo $sumaIsc; ?></cbc:TaxAmount>
      <cac:TaxCategory>
        <cac:TaxScheme>
          <cbc:ID><?php echo $idIsc; ?></cbc:ID>
          <cbc:Name><?php echo $codIsc; ?></cbc:Name>
          <cbc:TaxTypeCode><?php echo $codExtIsc; ?></cbc:TaxTypeCode>
        </cac:TaxScheme>
      </cac:TaxCategory>  
    </cac:TaxSubtotal>
  </cac:TaxTotal>
<?php endif; ?>
  <cac:TaxTotal>  
    <cbc:TaxAmount currencyID="<?php echo $moneda; ?>"><?php echo $sumaOtros; ?></cbc:TaxAmount>
    <cac:TaxSubtotal>
      <cbc:TaxAmount currencyID="<?php echo $moneda; ?>"><?php echo $sumaOtros; ?></cbc:TaxAmount>
      <cac:TaxCategory>
        <cac:TaxScheme>
          <cbc:ID><?php echo $idOtr; ?></cbc:ID>
          <cbc:Name><?php echo $codOtr; ?></cbc:Name>
          <cbc:TaxTypeCode><?php echo $codExtOtr; ?></cbc:TaxTypeCode>
        </cac:TaxScheme>
      </cac:TaxCategory>  
    </cac:TaxSubtotal>
  </cac:TaxTotal>
      
  <cac:RequestedMonetaryTotal>
    <cbc:ChargeTotalAmount currencyID="<?php echo $moneda; ?>"><?php echo $sumaOtrosCargos; ?></cbc:ChargeTotalAmount>
    <cbc:PayableAmount currencyID="<?php echo $moneda; ?>"><?php echo $sumaImporteVenta; ?></cbc:PayableAmount>
  </cac:RequestedMonetaryTotal>
  
  <?php foreach($listaDetalle as $detalle): ?>
  <cac:DebitNoteLine>
  <cbc:ID><?php echo $detalle['lineaSwf']; ?></cbc:ID>
  <cbc:DebitedQuantity unitCode="<?php echo $detalle['unidadMedida']; ?>"><?php echo $detalle['cantItem']; ?></cbc:DebitedQuantity>
  <cbc:LineExtensionAmount currencyID="<?php echo $moneda; ?>"><?php echo $detalle['valorVentaItem']; ?></cbc:LineExtensionAmount>
  <cac:PricingReference>
    <?php if( es($detalle['monto'])): ?>
<cac:AlternativeConditionPrice>
        <cbc:PriceAmount currencyID="<?php echo $moneda; ?>"><?php echo $detalle['monto']; ?></cbc:PriceAmount>
        <cbc:PriceTypeCode><?php echo $detalle['tipoCodigoMonedaSwf']; ?></cbc:PriceTypeCode>
    </cac:AlternativeConditionPrice>
    <?php else: ?>
    <cac:AlternativeConditionPrice>
      <cbc:PriceAmount currencyID="<?php echo $moneda; ?>"><?php echo $detalle['precioVentaUnitarioItem']; ?></cbc:PriceAmount>
      <cbc:PriceTypeCode><?php echo $tipoCodigoMonedaSwf; ?></cbc:PriceTypeCode>
    </cac:AlternativeConditionPrice>
    <?php endif; ?> 
  </cac:PricingReference>
   
  <cac:TaxTotal>
    <cbc:TaxAmount currencyID="<?php echo $moneda; ?>"><?php echo $detalle['montoIgvItem']; ?></cbc:TaxAmount>
    <cac:TaxSubtotal>
      <cbc:TaxableAmount currencyID="<?php echo $moneda; ?>"><?php echo $detalle['montoIgvItem']; ?></cbc:TaxableAmount>
      <cbc:TaxAmount currencyID="<?php echo $moneda; ?>"><?php echo $detalle['montoIgvItem']; ?></cbc:TaxAmount>
      <cac:TaxCategory>
        <cbc:TaxExemptionReasonCode><?php echo $detalle['afectaIgvItem']; ?></cbc:TaxExemptionReasonCode>
        <cac:TaxScheme>
          <cbc:ID><?php echo $idIgv; ?></cbc:ID>
          <cbc:Name><?php echo $codIgv; ?></cbc:Name>
          <cbc:TaxTypeCode><?php echo $codExtIgv; ?></cbc:TaxTypeCode>
        </cac:TaxScheme>  
      </cac:TaxCategory>
    </cac:TaxSubtotal>
  </cac:TaxTotal>
<?php $sumaIscLinea=$detalle['montoIscItem']; ?>
<?php if( ($sumaIscLinea > 0)): ?>
<cac:TaxTotal>  
    <cbc:TaxAmount currencyID="<?php echo $moneda; ?>"><?php echo $detalle['montoIscItem']; ?></cbc:TaxAmount>
    <cac:TaxSubtotal>
      <cbc:TaxableAmount currencyID="<?php echo $moneda; ?>"><?php echo $detalle['montoIscItem']; ?></cbc:TaxableAmount>
      <cbc:TaxAmount currencyID="<?php echo $moneda; ?>"><?php echo $detalle['montoIscItem']; ?></cbc:TaxAmount>
      <cac:TaxCategory>
        <cbc:TierRange><?php echo $detalle['tipoSistemaIsc']; ?></cbc:TierRange>
        <cac:TaxScheme>
          <cbc:ID><?php echo $idIsc; ?></cbc:ID>
          <cbc:Name><?php echo $codIsc; ?></cbc:Name>
          <cbc:TaxTypeCode><?php echo $codExtIsc; ?></cbc:TaxTypeCode>
        </cac:TaxScheme>
      </cac:TaxCategory>
    </cac:TaxSubtotal>
  </cac:TaxTotal>
 <?php endif; ?>
  <cac:Item>
    <cbc:Description><?php echo $detalle['desItem']; ?></cbc:Description>
    <cac:SellersItemIdentification>
        <cbc:ID><?php echo $detalle['codiProducto']; ?></cbc:ID>
    </cac:SellersItemIdentification>
    <cac:AdditionalItemIdentification>
        <cbc:ID><?php echo $detalle['codiSunat']; ?></cbc:ID>
    </cac:AdditionalItemIdentification>
  </cac:Item>
  <cac:Price>
    <cbc:PriceAmount currencyID="<?php echo $moneda; ?>"><?php echo $detalle['valorUnitario']; ?></cbc:PriceAmount>
  </cac:Price>
  </cac:DebitNoteLine>
  <?php endforeach; ?>
  
</DebitNote>